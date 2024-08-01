<?php

namespace App\Command;

use App\Entity\Blockchain;
use App\Service\NFTImporterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:tin',
    description: 'Add a short description for your command',
)]
class TinCommand extends Command
{
    public function __construct(
        protected readonly NFTImporterService     $nftImporterService,
        protected readonly EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $avalancheChain = $this->entityManager->getRepository(Blockchain::class)->findOneBy(['chainId' => 43113]);

        // ERC-721 https://thirdweb.com/avalanche-fuji/0x9bD769aC599677177354c65f6988Fd1F8689c721
        $this->nftImporterService->resolveAndAddToQueue('0x9bD769aC599677177354c65f6988Fd1F8689c721', $avalancheChain);

        // ERC-1155 https://thirdweb.com/avalanche-fuji/0x600C1F992939AF993bD030693fD41cbBF24230E5
        $this->nftImporterService->resolveAndAddToQueue('0x34c99E6ff4a974dc0800f71cC8d61080e94f6Bff', $avalancheChain);

        return Command::SUCCESS;
    }
}
