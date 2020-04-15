import styled from 'styled-components'

export default styled.div`
  // Do not overlap with the scope icon.
  width: 100%;
  padding-right: 40px;
  font-size: 12px;
  margin-top: 2px;
  text-align: justify;
  
  &:first-letter {
    text-transform: capitalize;
  }
`
