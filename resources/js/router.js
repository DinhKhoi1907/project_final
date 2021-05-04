import Vue from 'vue';
import Router from 'vue-router';
Vue.use(Router);

// import cac components vo day
import firstPage from './components/pages/firstPage.vue';
import hooks from './components/pages/basic/hooks.vue';
import methods from './components/pages/basic/methods.vue';

// admin project pages
import home from './components/pages/home.vue';
import tags from './admin/pages/tags.vue';
import category from './admin/pages/category.vue';

const routes = [
    //project router ..
    {
        path: '/',
        component: home,
    },
    {
        path: '/tags',
        component: tags
    },
    {
        path: '/category',
        component: category
    },
]

// register routes
export default new Router({
    mode: 'history',
    routes
});