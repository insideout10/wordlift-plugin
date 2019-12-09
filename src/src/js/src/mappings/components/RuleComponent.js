import React from 'react'
import Select from 'react-select'

export default class RuleComponent extends React.Component {
    constructor(props) {
        super(props)
        console.log(props + " from rule component")
    }
    render() {
        return (
                <div className="wl-container">
                    <div className="wl-col">
                        <Select value={this.props.field_one_options[0]} options={this.props.field_one_options} className="wl-field-one-select wl-form-select">
                        </Select>
                    </div>
                    <div className="wl-col">
                        <Select options={this.props.logic_field_options} className="wl-field-logic wl-form-select">
                        </Select>
                    </div>
                    <div className="wl-col">
                        <Select options={this.props.field_two_options} className="wl-field-two-select wl-form-select">
                        </Select>
                    </div>
                    <div className="wl-col">
                        <button className="button action"> And </button>
                    </div>
                </div>

        )
    }
}