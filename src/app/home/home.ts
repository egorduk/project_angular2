import { Component } from '@angular/core';
import { CORE_DIRECTIVES } from '@angular/common';
import { Http, Headers } from '@angular/http';
import { Router, ROUTER_DIRECTIVES } from '@angular/router';
import { AuthHttp } from 'angular2-jwt/angular2-jwt';

//const styles = require('./home.css');
//const template = require('./home.html');

@Component({
    selector: 'home',
    directives: [ CORE_DIRECTIVES, ROUTER_DIRECTIVES ],
    //template: template,
    //styles: [ styles ]
    //template: 'home.html'
    templateUrl: 'app/home/home.html'
})

export class Home {
    jwt: string;
    decodedJwt: string;
    response: string;
    api: string;
    _serverUrl: string = '';

    constructor(public router: Router, public http: Http, public authHttp: AuthHttp) {
        this.jwt = localStorage.getItem('id_token');
        this.decodedJwt = this.jwt && window.jwt_decode(this.jwt);
        this._serverUrl = 'http://localhost:80/project_angular2';
        this.getAllUserPictures();
    }

    logout() {
        localStorage.removeItem('id_token');
        this.router.navigate(['/login']);
    }

    _callApi(type, url) {
        this.response = null;
        if (type === 'Anonymous') {
            // For non-protected routes, just use Http
            this.http.get(url)
                .subscribe(
                    response => this.response = response.text(),
                    error => this.response = error.text(),
                    () => console.log('Request Complete')
            );
        }
        if (type === 'Secured') {
            // For protected routes, use AuthHttp
            this.authHttp.get(url)
                .subscribe(
                    response => this.response = response.text(),
                    error => this.response = error.text()
            );
        }
    }

    getAllUserPictures() {
        this.authHttp.get(this._serverUrl + '/api/get_all_user_pictures')
            .subscribe(
            response => this.response = response.text(),
            error => this.response = error.text()
        );
    }

    friends(event) {
        event.preventDefault();
        this.router.navigate(['/friends']);
    }
}
