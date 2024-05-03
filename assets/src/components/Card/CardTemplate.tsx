import {
    Card,
    CardBody,
    CardFooter,
    Typography,
    IconButton,
    Button,} from "@material-tailwind/react";
import { FaHeart } from "react-icons/fa";
import "../../styles/card.css";


interface CardPropsType {
    name: string;
    author: string;
    bid: number;
    currency: string;
}

export default function CardTemplate({name, bid, author, currency}: CardPropsType) {
    return (
        <Card className="mt-6 w-96 card-wrapper">
            <div className="card-img-wrapper relative">
                <img
                    src="https://images.unsplash.com/photo-1540553016722-983e48a2cd10?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=800&q=80"
                    alt="card-image"
                    className="object-cover h-96"
                />
            </div>
            <CardBody>
                <CardBody className="p-0 flex justify-between items-center">
                    <Typography variant="h5" className="mb-0">
                        {name}
                    </Typography>
                    <IconButton variant="gradient" className="rounded-full">
                        <FaHeart/>
                    </IconButton>
                </CardBody>
            </CardBody>
            <CardBody className="flex justify-between pt-0">
                <CardBody className="flex card-author p-0 items-center">
                    <CardBody className="p-0 w-16">
                        <img
                            src="https://forkast.news/wp-content/uploads/2022/03/NFT-Avatar.png"
                            alt="card-image"
                            className="rounded-full w-12 h-12 mr-2"
                        />
                    </CardBody>
                    <CardBody className="p-0">
                        <Typography className="font-bold">
                            Creator
                        </Typography>
                        <Typography>
                            {author}
                        </Typography>
                    </CardBody>
                </CardBody>
                <CardBody className="card-bid p-0">
                    <Typography className="font-bold">
                        Current bid
                    </Typography>
                    <Typography>
                        {bid}
                        {currency}
                    </Typography>
                </CardBody>
            </CardBody>
            <CardFooter className="pt-0">
                <Button className="
                w-full ease-in font-bold">Place Bid</Button>
            </CardFooter>
        </Card>
    );
}
