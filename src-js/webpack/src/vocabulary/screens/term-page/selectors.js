import {  select } from "redux-saga/effects";

export const getTermId = (state) => {
    return state.termId
}

export const getApiConfig = (state) => {
    return state.apiConfig
}