import { provideRouter, RouterConfig } from '@angular/router';

import { CustomersRoutes } from './customers/customers.routes';
import { CustomerRoutes } from './customer/customer.routes';

import { LoginComponent } from './auth/login.component';
//import { HomeComponent } from './home.component';

import {Home} from '../home/home';
import {Login} from '../login/login';
import {Signup} from '../signup/signup';

export const App_Routes: RouterConfig = [
...CustomersRoutes,
...CustomerRoutes,
    { path: 'customers', pathMatch: 'full', redirectTo: '/customers' }, //redirect to home page
    { path: 'login', component: LoginComponent, name: 'Login' },
    { path: '/', redirectTo: ['/home'] },
    { path: '/home', component: Home, as: 'Home' },
    { path: '/login', component: Login, as: 'Login' },
    { path: '/signup', component: Signup, as: 'Signup' }
   // { path: '/', component: HomeComponent, name: 'Home', useAsDefault: true },
    //{ path: '**', component: PageNotFoundComponent }
];

export const APP_ROUTER_PROVIDERS = [
    provideRouter(App_Routes)
];
