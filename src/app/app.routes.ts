// - Routes instead of RouteConfig
// - RouterModule instead of provideRoutes
import { Routes, RouterModule } from '@angular/router';

import { Home } from './home/home';
import { Login } from './login/login';
import { Signup } from './signup/signup';
import { AuthGuard } from './common/auth.guard';
import { Friends } from './friends/friends';
import { User } from './user/user';

const routes: Routes = [
    { path: '',       component: Login },
    { path: 'login',  component: Login },
    { path: 'signup', component: Signup },
    { path: 'home',   component: Home, canActivate: [AuthGuard] },
    { path: 'friends', component: Friends },
    { path: 'user/:id', component: User },
    { path: '**',     component: Login }
];

// - Updated Export
export const routing = RouterModule.forRoot(routes);