import { Component } from '@angular/core';
import { Router, ROUTER_DIRECTIVES } from '@angular/router';
import { CORE_DIRECTIVES } from '@angular/common';
import { Http, Headers } from '@angular/http';
import { contentHeaders } from '../common/headers';

//const styles   = require('./login.css');
//const template = require('./login.html');

@Component({
    selector: 'login',
    directives: [ ROUTER_DIRECTIVES, CORE_DIRECTIVES ],
    //template: `<router-outlet></router-outlet>`,
    //template: template,
    //styles: [ styles ]
    //template: 'login.html'
    templateUrl: 'app/login/login.html'
})
export class Login {

    _serverUrl: string = '';
    _hideError: boolean = true;

    constructor(public router: Router, public http: Http) {
        this._serverUrl = 'http://localhost:80/project_angular2';
    }

    login(event, email, password) {
        //console.log(username);
        event.preventDefault();
        let body = JSON.stringify({ email, password });
        //console.log(body);
        // let body = JSON.stringify({ username: 'u', password: 'p' });
        //this.http.post('http://localhost:3001/sessions/create', body, { headers: contentHeaders })
        this.http.post(this._serverUrl + '/api/create_session', body, { headers: contentHeaders })
            .subscribe(
                response => {
                //this._hideError = response;
                localStorage.setItem('id_token', response.json().id_token);
                this.router.navigate(['/home']);
            },
                error => {
                //alert(error.text());
                console.log(error.text());
            }
        );

        /*  this.http.get(this._serverUrl + '?action=test')
         .subscribe(
         response => {
         //localStorage.setItem('id_token', response.json().id_token);
         //this.router.navigate(['/home']);
         console.log(response);
         },
         error => {
         //alert(error.text());
         console.log(error.text());
         }
         );*/
    }

    signup(event) {
        event.preventDefault();
        this.router.navigate(['/signup']);
    }
}