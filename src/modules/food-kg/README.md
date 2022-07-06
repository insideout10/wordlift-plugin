## Requirements

1. when the WL key is changed, check the connected datasets
2. when the connected datasets include the food kg, schedule itself daily
3. when the connected datasets do not include the food kg, unschedule itself
4. when the schedule runs, show a message that we're running, lift the ingredients by sending the list of ingredients to
   the platform, receive and store the json-lds, then update the message with the results
5. when the json-ld is requested, inject the ingredients json-ld

## Hooks

### Food KG module

| Hook           | Type   | Description      |
|----------------|--------|------------------|
| wl_food_kg_run | action | Scheduled action |

### WordLift plugin

| Hook           | Type   | Description                       |
|----------------|--------|-----------------------------------|
| wl_key_updated | action | Raised when the WL key is updated |

## UI

### Status Message

### Ingredients Page
