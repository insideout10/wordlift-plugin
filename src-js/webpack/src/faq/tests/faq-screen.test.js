import React from 'react';
import renderer from 'react-test-renderer';
import FaqScreen from "../components/faq-screen";

it('renders correctly', () => {
    const tree = renderer
        .create(<FaqScreen></FaqScreen>)
        .toJSON();
    expect(tree).toMatchSnapshot();
});