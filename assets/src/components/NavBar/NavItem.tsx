import {Typography} from "@material-tailwind/react";
import React from "react";

interface NavItemPropsType {
    label: string;
}

export default function NavItem({label}: NavItemPropsType) {
    return (
        <a href="#">
            <Typography as="li" color="blue-gray" className="p-1 font-medium">
                {label}
            </Typography>
        </a>
    );
}
