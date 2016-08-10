import { Component, Input } from '@angular/core';
import { Router, ROUTER_DIRECTIVES } from '@angular/router';
import { CORE_DIRECTIVES } from '@angular/common';
import { Http, Headers } from '@angular/http';
import { contentHeaders } from '../common/headers';
import { IPicture } from '../shared/interfaces';
import { AuthHttp } from 'angular2-jwt/angular2-jwt';

import { Observable } from 'rxjs/Observable';
import {Observer} from 'rxjs/Observer';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';

@Component({
    selector: 'friends',
    directives: [ ROUTER_DIRECTIVES, CORE_DIRECTIVES ],
    templateUrl: 'app/friends/friends.html'
})

export class Friends {

    _serverUrl: string = '';
    jwt: string = '';
    //@Input() pictures: IPicture[] = [];
    pictures: IPicture[];

    constructor(public router: Router, public http: Http, public authHttp: AuthHttp) {
        this._serverUrl = 'http://localhost:80/project_angular2';
        //this.jwt = localStorage.getItem('id_token');
       // this.getFriendsPictures();
    }

    getFriendsPictures(): Observable<ICustomer[]> {
        /*this.authHttp.get(this._serverUrl + '/api/get_friends_pictures')
            .subscribe(
            response => {
                this.pictures = response.json();
                console.log(this.pictures);

            },
            error => this.response = error.text()
        );*/

        this.http.get(this._serverUrl + '/api/get_friends_pictures')
            .map((res: Response) => {
                //this.pictures = res.json();
                console.log(res.json());
                //return this.user;
                return res.json();
            }).subscribe((pictures: IPicture[]) => {
                console.log(pictures);
            });
    }
}