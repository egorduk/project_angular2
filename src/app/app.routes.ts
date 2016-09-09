import { Routes, RouterModule } from '@angular/router';

import { LoginComponent } from './login/login.component';
import { SignupComponent } from './signup/signup.component';
import { AuthGuard } from './common/auth.guard';
import { FriendsComponent } from './friends/friends.component';
import { UserComponent } from './user/user.component';

const routes: Routes = [
    { path: '',       component: LoginComponent },
    { path: 'login',  component: LoginComponent },
    { path: 'signup', component: SignupComponent },
    { path: 'friends', component: FriendsComponent, canActivate: [AuthGuard] },
    { path: 'user/:login', component: UserComponent, canActivate: [AuthGuard] },
    { path: '**', component: LoginComponent }
];

// - Updated Export
export const routing = RouterModule.forRoot(routes);