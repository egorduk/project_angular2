import { Component, Input } from '@angular/core';
import { Router, ROUTER_DIRECTIVES } from '@angular/router';
import { CORE_DIRECTIVES } from '@angular/common';
import { Http, Headers } from '@angular/http';
import { contentHeaders } from '../common/headers';
import { AuthHttp } from 'angular2-jwt/angular2-jwt';

import { Observable } from 'rxjs/Observable';
import {Observer} from 'rxjs/Observer';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import { DataService } from '../common/service/data.service';
import { IPicture, IUser } from '../common/interfaces';

@Component({
    selector: 'friends',
    directives: [ ROUTER_DIRECTIVES, CORE_DIRECTIVES ],
    templateUrl: 'app/friends/friends.html'
})

export class Friends {

    _serverUrl: string = '';
    //jwt: string = '';
    //@Input() pictures: IPicture[] = [];
    pictures: IPicture[];
    users: IUser[];

    constructor(public router: Router, public http: Http, private authHttp: AuthHttp, public dataService: DataService) {
        this._serverUrl = 'http://localhost:80/project_angular2';
        //this.jwt = localStorage.getItem('id_token');
        // this.getFriendsPictures();
    }

    getFriendsPictures() {
        this.dataService.getFriendsPictures()
            .subscribe((pictures: IPicture[]) => {
                //console.log(pictures);
                this.pictures = pictures;
            });
        this.getUnfollowUsers();
    }

    getUnfollowUsers() {
        this.dataService.getUnfollowUsers()
            .subscribe((users: IUser[]) => {
                console.log(users);
                if (!users.error) {
                    users.forEach((value: any, key: any) => {
                        users[key].pictures = value.pictures.split(',', 3);
                    });
                    this.users = users;
                } else {
                    this.users = null;
                }
            });
    }

    followUser(event, userId) {
        event.preventDefault();
        console.log(userId);
        this.dataService.followUser(userId)
            .subscribe((response: boolean) => {
                console.log(response);
                if (response.response) {
                    this.getUnfollowUsers();
                }
            });
    }

    getUnfollowUser() {
        this.dataService.getUnfollowUser()
            .subscribe((users: IUser) => {
               /* users.forEach((value: any, key: any) => {
                    users[key].pictures = value.pictures.split(',', 3);
                });*/
                console.log(users);
                //this.users = users;
            });
    }
}