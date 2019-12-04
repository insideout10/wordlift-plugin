import styled, {css} from 'styled-components';

const Lead = styled.div`
    ${props => props.open && css`
        border-top : 0 !important;
        border-bottom: 5px solid #007aff;
    `}
`;

export default Lead;
