import {ClassAttributes, createElement, forwardRef, InputHTMLAttributes} from "react";
import {IconBaseProps} from "react-icons";

type Icon = {
    as: string,
}

export default forwardRef((props: Icon & IconBaseProps, ref) => {
    const childProps = {...props, as: undefined}

    if (!(props) || props.as === undefined) {
        throw new Error('Icon component requires an "as" prop')
    }

    return (
        <span ref={ref}>
                {createElement(props.as, childProps)}
        </span>
    )
})
