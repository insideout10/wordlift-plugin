function addActiveStateToEntity(entities) {

    return entities.map((entity, index)=> {
        entity.isActive = index === 0;
        return entity;
    })
}

export function convertApiResponseToUiObject( apiResponse ) {
    return apiResponse.map((tag) => {
        tag.entities = addActiveStateToEntity(tag.entities)
        return tag
    })
}