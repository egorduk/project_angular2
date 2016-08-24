import { Component, Input } from '@angular/core';
import { Router, ROUTER_DIRECTIVES } from '@angular/router';
import { CORE_DIRECTIVES } from '@angular/common';
import { Http } from '@angular/http';
import { Observable } from 'rxjs/Observable';
import {Observer} from 'rxjs/Observer';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import { DataService } from '../common/service/data.service';

@Component({
    selector: 'user',
    directives: [ ROUTER_DIRECTIVES, CORE_DIRECTIVES ],
    styleUrls: ['app/user/style.css'],
    templateUrl: 'app/user/user.html'
})

export class User {


    constructor(public router: Router, public http: Http, private dataService: DataService) {
      /*  let token = localStorage.getItem('id_token');
        let data = window.jwt_decode(token);
        this._userId = data.uid;*/
    }
}