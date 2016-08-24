import { RouterConfig } from '@angular/router';
import { Home } from './home/home';
import { Login } from './login/login';
import { Signup } from './signup/signup';
import { AuthGuard } from './common/auth.guard';
import { Friends } from './friends/friends';
import { User } from './user/user';

export const routes: RouterConfig = [
    { path: '',       component: Login },
    { path: 'login',  component: Login },
    { path: 'signup', component: Signup },
    { path: 'home',   component: Home, canActivate: [AuthGuard] },
    { path: 'friends', component: Friends },
    { path: 'user/:login', component: User },
    { path: '**',     component: Login }
];