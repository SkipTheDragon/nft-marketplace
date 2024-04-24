<?php

namespace App\Service;

use App\Architecture\EAccountWallet;
use App\Architecture\ENFTTypes;
use App\Entity\AccountWallet;
use App\Entity\Blockchain;
use App\Entity\NFT;
use App\Entity\NFTImportItem;
use App\Service\NFTImporter\RpcProviderService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Web3\Contract;

readonly class NFTImporterService
{
    private array $supportedInterfaces;
    private array $defaultContractIdentifiers;
    private array $mappedContractDataToTokenType;

    public function __construct(
        protected HttpClientInterface    $client,
        protected RpcProviderService     $rpcProviderService,
        protected EntityManagerInterface $entityManager
    )
    {
        // Modify these 2 arrays to support more NFT types.
        $this->supportedInterfaces = [
            ENFTTypes::EDITION->value => '0xd9b67a26',
            ENFTTypes::COLLECTION->value => '0x80ac58cd'
        ];

        $this->defaultContractIdentifiers = [
            ENFTTypes::EDITION->value => 'erc_1155_default_thirdweb',
            ENFTTypes::COLLECTION->value => 'erc_721_default_thirdweb'
        ];

        $this->mappedContractDataToTokenType = [
            ENFTTypes::EDITION->value => [
                'supply' => 'nextTokenIdToMint',
                'ownerOf' => 'getRoyaltyInfoForToken',
                'uri' => 'uri'

            ],
            ENFTTypes::COLLECTION->value => [
                'supply' => 'totalSupply',
                'ownerOf' => 'ownerOf',
                'uri' => 'tokenURI'
            ]
        ];
    }

    public function getContractType(string $contractAddress, Blockchain $blockchain): ?string
    {
        $abi = [
            "abi" => [
                'inputs' => [
                    [
                        'internalType' => 'bytes4',
                        'name' => 'interfaceId',
                        'type' => 'bytes4',
                    ],
                ],
                'name' => 'supportsInterface',
                'outputs' => [
                    [
                        'internalType' => 'bool',
                        'name' => '',
                        'type' => 'bool',
                    ],
                ],
                'stateMutability' => 'view',
                'type' => 'function',
            ]
        ];

        $contract = new Contract($this->rpcProviderService->getFastestProvider($blockchain)['provider'], $abi);

        foreach ($this->supportedInterfaces as $tokenName => $interface) {
            $supportsInterface = "";
            $contract->at($contractAddress)->call('supportsInterface', $interface, function ($err, $result) use (&$supportsInterface) {
                $supportsInterface = $result[0];
            });
            if ($supportsInterface) {
                return $tokenName;
            }
        }

        return null;
    }

    /**
     * @param string $contractAddress
     * @param Blockchain $blockchain
     * @param string|null $identifier - Unique string to identify the contract abi
     * @return void
     * @throws Exception
     */
    public function resolveAndAddToQueue(string $contractAddress, Blockchain $blockchain, ?string $identifier = null): void
    {
        $contractType = $this->getContractType($contractAddress, $blockchain);

        if ($contractType === null) {
            throw new Exception('Contract type not supported. Abi not found please add it manually.');
        }

        /**
         * @var \App\Entity\Contract::class $contract
         */
        $contractEntity = null;

        // If identifier is not provided, use default contracts added by admin.
        if ($identifier === null) {
            $contractEntity = $this->entityManager->getRepository(\App\Entity\Contract::class)->findOneBy(['identifier' => $this->defaultContractIdentifiers[$contractType]]);
        } else {
            $contractEntity = $this->entityManager->getRepository(\App\Entity\Contract::class)->findOneBy(['identifier' => $identifier]);
        }

        if ($contractEntity === null) {
            throw new Exception('Contract not found. Please add it manually.');
        }

        // Initialize contract
        $contract = new Contract($this->rpcProviderService->getFastestProvider($blockchain)['provider'], $contractEntity->getAbi());
        $currentContract = $contract->at($contractAddress);

        // Get all tokens

        $currentContract->call($this->mappedContractDataToTokenType[$contractType]['supply'], function ($err, $result) use (&$totalSupply) {
            $totalSupply = intval($result[0]->toString());
        });

        /**
         * If contract type is edition, add 1 to total supply
         */
        if ($contractType === ENFTTypes::EDITION->value) {
            $totalSupply += 1;
        }

        // Get all token uris
        for ($i = 0; $i < $totalSupply; $i++) {
            $currentContract->call($this->mappedContractDataToTokenType[$contractType]['uri'], $i, function ($err, $result) use (&$metadataUri) {
                $metadataUri = $result[0];
            });

            if(empty($metadataUri)) { // TODO: handle import error
                continue;
            }

            // TODO: Replace with custom postgresql query:

            $nftEntity = new NFT();
            $nftEntity->setAddress($contractAddress);
            $nftEntity->setTokenId($i);
            $nftEntity->setBlockchain($blockchain);
            $nftEntity->setType(ENFTTypes::fromValue($contractType));

            $currentContract->call($this->mappedContractDataToTokenType[$contractType]['ownerOf'], $i, function ($err, $result) use (&$owner) {
                $owner = $result[0];
            });

            $existingWallet = $this->entityManager->getRepository(AccountWallet::class)->findOneBy(['address' => $owner]);

            if (!$existingWallet) {
                $wallet = new AccountWallet();
                $wallet->setAddress($owner);
                $wallet->setType(EAccountWallet::STUB_FROM_IMPORT);
                $this->entityManager->persist($wallet);
                $nftEntity->addOwner($wallet);
            } else {
                $nftEntity->addOwner($existingWallet);
            }

            $nftImportItemEntity = new NFTImportItem();

            $nftImportItemEntity->setMetadataUri($metadataUri);
            $nftImportItemEntity->setNft($nftEntity);

            $this->entityManager->persist($nftEntity);
            $this->entityManager->persist($nftImportItemEntity);

        }

        $this->entityManager->flush();
    }

}
