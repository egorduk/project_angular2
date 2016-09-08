import { Injectable } from '@angular/core';
import { Router, CanActivate } from '@angular/router';
import { tokenNotExpired, JwtHelper } from 'angular2-jwt/angular2-jwt';

import { AUTH_PROVIDERS } from 'angular2-jwt/angular2-jwt';

@Injectable()
export class AuthGuard implements CanActivate {

    jwtHelper: JwtHelper = new JwtHelper();

    constructor(private router: Router) {}

    canActivate() {
        var token = localStorage.getItem('id_token');

       /* console.log(
            //token,
            this.jwtHelper.decodeToken(token),
            this.jwtHelper.getTokenExpirationDate(token),
            this.jwtHelper.isTokenExpired(token)
        );*/

        if (tokenNotExpired()) {
            return true;
        }

        this.router.navigate(['/login']);

        return false;
    }
}
