import { provideRouter, RouterConfig } from '@angular/router';

import { CustomersRoutes } from './customers/customers.routes';
import { CustomerRoutes } from './customer/customer.routes';

import { LoginComponent } from './auth/login.component';
//import { HomeComponent } from './home.component';

export const App_Routes: RouterConfig = [
...CustomersRoutes,
...CustomerRoutes,
    { path: 'customers', pathMatch: 'full', redirectTo: '/customers' }, //redirect to home page
    { path: 'login', component: LoginComponent, name: 'Login' },
   // { path: '/', component: HomeComponent, name: 'Home', useAsDefault: true },
    //{ path: '**', component: PageNotFoundComponent }
];

export const APP_ROUTER_PROVIDERS = [
    provideRouter(App_Routes)
];
