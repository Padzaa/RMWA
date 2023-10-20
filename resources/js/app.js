import { createApp, h } from 'vue'
import {createInertiaApp,Link,Head} from "@inertiajs/inertia-vue3";
import * as styles from  'vuetify/styles';
import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components';
import '@mdi/font/css/materialdesignicons.min.css';
import * as directives from 'vuetify/directives';
import Layout from "./Shared/Layout.vue";
import {Inertia} from "@inertiajs/inertia";
import Filter from "./Shared/Filter.vue";
import Filtering from "./Shared/Filtering.vue";
import Alert from "./Shared/Alert.vue";

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
        const page = pages[`./Pages/${name}.vue`];

        if(page){
            page.default.layout = Layout;

            return page;
        }else{

            return ()=> {
                Inertia.get('/');
            }
        }



    },
    setup({el, App, props, plugin}) {
        createApp({render: () => h(App, props)})
            .use(plugin)
            .component("Link",Link)
            .component("Head",Head)
            .component("Filter",Filter)
            .component("Filtering",Filtering)
            .component("Alert",Alert)
            .use(vuetify)
            .mount(el)
    },
    title: title => "RMWA - " + title,

});
