<template>
    <div class="filters">

        <div class="form-group">
            <div class="btn-group">
                <button type="button" class="btn btn-primary dropdown-toggle"
                        aria-haspopup="true" data-bs-toggle="dropdown" aria-expanded="false">
                    <slot name="button_name"></slot>
                </button>
                <div class="dropdown-menu">
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
        categories: Array,
    }
}
</script>

<style scoped>
.filters {
    display: grid;
    padding: 1.5em 3em;
    height: 92px;
    width: fit-content;
}

.dropdown-menu {
    overflow: scroll;
    max-height: 400px;
}

.dropdown-toggle {
    padding: 0.5em 1.6em;
}
</style>
