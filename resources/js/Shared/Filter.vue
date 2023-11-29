<template>
    <div class="filters">

        <div class="form-group">
            <div class="btn-group">
                <button style="font-size: 1.1rem" type="button"
                        class="text-black  font-italic text-blue-accent-4 dropdown-toggle"
                        aria-haspopup="true" data-bs-toggle="dropdown" aria-expanded="false">
                    <slot name="button_name"></slot>
                </button>
                <div class="dropdown-menu" >
                    <slot name="filter_slot"></slot>


                </div>
            </div>
        </div>


    </div>
</template>

<script>
export default {
    name: "filters.vue",
    props: {
        categories: {
            type: [Object, Array],
        },
    }
}
</script>

<style scoped>
.filters {
    display: grid;
//padding: 1.5em 3em; //height: 92px; width: fit-content;
}

button {
    color: white;
    font-weight: 500;
}

.dropdown-menu {
    overflow-y: auto;
    max-height: 300px;
}

.dropdown-toggle {
    padding: 0.5em 1.6em;
}
</style>
