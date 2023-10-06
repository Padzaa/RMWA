import { createApp, h } from 'vue'
import {createInertiaApp,Link,Head} from "@inertiajs/inertia-vue3";
import Header from './Shared/Header.vue';
import * as styles from  'vuetify/styles';
import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components';
import '@mdi/font/css/materialdesignicons.min.css';

import * as directives from 'vuetify/directives';


const vuetify = createVuetify({
    components,
    directives,
styles,
    icons:{
        iconFont:'mdi'
    }
});


createInertiaApp({

    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`]
    },
    setup({el, App, props, plugin}) {
        createApp({render: () => h(App, props)})
            .use(plugin)
            .component("Link",Link)
            .component("Head",Head)
            .component("Header",Header)
            .use(vuetify)
            .mount(el)
    },
    title: title => "RMWA - " + title,
});
