import { Component } from '@angular/core';
import { Router, ROUTER_DIRECTIVES } from '@angular/router';
import { CORE_DIRECTIVES } from '@angular/common';
import { Http, Headers } from '@angular/http';
import { contentHeaders } from '../common/headers';

@Component({
    selector: 'friends',
    directives: [ ROUTER_DIRECTIVES, CORE_DIRECTIVES ],
    templateUrl: 'app/friends/friends.html'
})

export class Friends {

    constructor(public router: Router, public http: Http) {

    }
}