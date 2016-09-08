import { Routes, RouterModule } from '@angular/router';

import { HomeComponent } from './home/home.component';
import { LoginComponent } from './login/login.component';
import { SignupComponent } from './signup/signup.component';
import { AuthGuard } from './common/auth.guard';
import { Friends } from './friends/friends';
import { User } from './user/user';

const routes: Routes = [
    { path: '',       component: LoginComponent },
    { path: 'login',  component: LoginComponent },
    { path: 'signup', component: SignupComponent },
    { path: 'home',   component: HomeComponent, canActivate: [AuthGuard] },
    { path: 'friends', component: Friends },
    { path: 'user/:id', component: User },
    { path: '**', component: LoginComponent }
];

// - Updated Export
export const routing = RouterModule.forRoot(routes);