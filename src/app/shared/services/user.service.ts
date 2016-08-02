import { Injectable } from '@angular/core';
import {LocalStorage, SessionStorage} from "angular2-localstorage/WebStorage";
import { Http, Response, HTTP_PROVIDERS } from '@angular/http';
import { Observable } from 'rxjs/Observable';
import {Observer} from 'rxjs/Observer';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';


@Injectable()
export class UserService {

    private loggedIn = false;
    //@LocalStorage() public foo:string = '';

    constructor(private http: Http) {
        //this.loggedIn = !!localStorage.getItem('auth_token');
        //this.ls.set('someKey', 'someValue');
    }

    /*login(email, password) {
        let headers = new Headers();
        headers.append('Content-Type', 'application/json');

        return this.http
            .post(
            '/login',
            JSON.stringify({ email, password }),
            { headers }
        )
            .map(res => res.json())
            .map((res) => {
                if (res.success) {
                    localStorage.setItem('auth_token', res.auth_token);
                    this.loggedIn = true;
                }

                return res.success;
            });
    }

    logout() {
        localStorage.removeItem('auth_token');
        this.loggedIn = false;
    }

    isLoggedIn() {
        return this.loggedIn;
    }*/
}