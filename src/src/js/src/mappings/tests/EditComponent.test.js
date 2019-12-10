/**
 * @since 3.24.0
 * 
 * Tests for Edit Component
 */

import React from 'react'
import Adapter from 'enzyme-adapter-react-16';
import { shallow, configure, render } from 'enzyme'
configure({adapter: new Adapter()});

import EditComponent from '../components/EditComponent'

test("can render edit component", ()=> {
    shallow(<EditComponent />)
})