import Vue from 'vue';
import VueRouter from 'vue-router';

import homepage from '../components/homepage/homepage.vue';
import contact from '../components/pages/contact.vue';
import about from '../components/pages/about.vue';
import help from '../components/pages/help.vue';
import notFound from '../components/notFound.vue';

Vue.use(VueRouter);

const routes = new VueRouter({
	mode: 'history',
	routes: [
		{path: '*', component: notFound, meta: {title: '404 Not Found'}},
		{path: '/', component: homepage},
		{path: '/contact', component: contact, meta: {title: 'Contact'}},
		{path: '/about', component: about, meta: {title: 'About'}},
		{path: '/help', component: help, meta: {title: 'Help'}},
	]
});

routes.beforeEach(function(to, from, next) {
	if (to.meta && to.meta.title) {
		window.document.title = to.meta.title + ' | Short URL Generator';
		
	} else {
		window.document.title = 'Short URL Generator';
	}
	
	next();
});

export default routes;
