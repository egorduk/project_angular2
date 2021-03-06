import { Component } from '@angular/core';
import { Router, ROUTER_DIRECTIVES } from '@angular/router';
import { CORE_DIRECTIVES } from '@angular/common';
import { Http } from '@angular/http';
import { contentHeaders } from '../common/headers';

//const styles   = require('./signup.css');
//const template = require('./signup.html');

@Component({
    selector: 'signup',
    directives: [ CORE_DIRECTIVES, ROUTER_DIRECTIVES ],
    //template: template,
    // styles: [ styles ]
    templateUrl: 'app/signup/signup.html'
})
export class Signup {

    _serverUrl: string = '';

    constructor(public router: Router, public http: Http) {
        this._serverUrl = 'http://localhost:80/project_angular2';
    }

    signup(event, email, password) {
        event.preventDefault();
        let body = JSON.stringify({ email, password });
        this.http.post(this._serverUrl + '/api/create_user', body, { headers: contentHeaders })
            .subscribe(
                response => {
                localStorage.setItem('id_token', response.json().id_token);
                this.router.navigate(['/home']);
            },
                error => {
                //alert(error.text());
                console.log(error.text());
            }
        );
    }

    login(event) {
        event.preventDefault();
        this.router.navigate(['/login']);
    }

}