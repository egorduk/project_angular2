import { Component, OnInit } from '@angular/core';
import { Router, ROUTER_DIRECTIVES } from '@angular/router';
import { CORE_DIRECTIVES } from '@angular/common';
import { Http } from '@angular/http';
import { Observable } from 'rxjs/Observable';
import {Observer} from 'rxjs/Observer';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import { DataService } from '../common/service/data.service';
import { IUser } from '../common/interfaces';
//import { Subscription } from 'rxjs/Subscription';

@Component({
    selector: 'user',
    directives: [ ROUTER_DIRECTIVES, CORE_DIRECTIVES ],
    styleUrls: ['app/user/style.css'],
    templateUrl: 'app/user/user.html'
})

export class User implements OnInit {

    //private sub: Subscription;
    private _userLogin: string = '';
    private _userId: number = 0;
    private _isCurrentUser: boolean = false;
    private user: IUser[];

    constructor(private router: Router, private http: Http, private dataService: DataService) {
        let token = localStorage.getItem('id_token');
        let data = window.jwt_decode(token);
        this._userId = data.uid;
       // this.router.navigate(['/hero', hero.id]);
        //this._userLogin = this.router.url.split('/')[2];
        //console.log(this._userLogin);
    }

    ngOnInit() {
        this._userLogin = this.router.url.split('/')[2];
        //let ul = this.router.url.split('/')[2];
        this.getUserInfo();
    }

    getUserInfo() {
        this.dataService.getUserInfo(this._userLogin)
            .subscribe((user: IUser[]) => {
                if (user.response) {
                    console.log(user);
                    if (user.user) {
                        this.user = user.user;

                        if (user.id == this._userId) {
                            this._isCurrentUser = true;
                        }

                        console.log(this.user);
                    }
                } else {
                    this.router.navigate(['/home']);
                }
            });
    }
}