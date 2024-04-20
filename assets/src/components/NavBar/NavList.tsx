import NavItem from "./NavItem";

export default function NavList() {
    return (
        <ul className="mb-4 mt-2 flex flex-col gap-3 lg:mb-0 lg:mt-0 lg:flex-row lg:items-center lg:gap-8">
            <NavItem label="About Us" />
            <NavItem label="Pricing" />
            <NavItem label="Contact Us" />
        </ul>
    );
}
