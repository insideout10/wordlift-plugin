import styled from 'styled-components'

export default styled.div`
  background-image: url(${props => props.src});
  width: 40px;
  height: 40px;
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center;
  border: 1px solid #efefef;
  display: inline-block;
  margin-right: 10px;
  vertical-align: top;
  ${props => props.src ? '' : 'border: 0;' }
`
