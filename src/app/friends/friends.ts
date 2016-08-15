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
import {ImageModal} from 'angular2-image-popup/image-modal-popup';
//import {ImageModal} from 'angular2-image-popup/angular2-image-popup';

@Component({
    selector: 'friends',
    directives: [ ROUTER_DIRECTIVES, CORE_DIRECTIVES, ImageModal ],
    templateUrl: 'app/friends/friends.html'
})

export class Friends {

    pictures: IPicture[];
    users: IUser[];
    _selectedPicture: string = '';
    _isLiked: boolean = false;

    openModalWindow: boolean = false;
    imagePointer:number;
    images = [
        { thumb: '', img: '../../uploads/pictures/1.jpg', description: 'Image 1' }
        { thumb: '', img: '../../uploads/pictures/2.jpg', description: 'Image 2' }
    ];

    constructor(public router: Router, public http: Http, private authHttp: AuthHttp, private dataService: DataService) {
        //this.jwt = localStorage.getItem('id_token');
        // this.getFriendsPictures();
        //console.log();
    }

    getFriendsPictures() {
        this.dataService.getFriendsPictures()
            .subscribe((pictures: IPicture[]) => {
                //console.log(pictures);
                if (pictures.response) {
                    this.pictures = pictures.pictures;
                } else {
                    this.pictures = null;
                }
            });
        this.getUnfollowUsers();
    }

    getUnfollowUsers() {
        this.dataService.getUnfollowUsers()
            .subscribe((users: IUser[]) => {
                //console.log(users);
                if (users.response) {
                    users.users.forEach((value: any, key: any) => {
                        users.users[key].pictures = value.pictures.split(',', 3);
                    });
                    this.users = users.users;
                } else {
                    this.users = null;
                }
            });
    }

    followUser(event, userId) {
        event.preventDefault();
        //console.log(userId);
        this.dataService.followUser(userId)
            .subscribe((response: boolean) => {
                console.log(response);
                if (response.response) {
                    this.getFriendsPictures();
                }
            });
    }

    closePopup() {
        this.openModalWindow = false;
    }

    openPopup(picture) {
        this._selectedPicture = picture;
        this.openModalWindow = true;
    }

    likePicture(event, picture) {
        event.preventDefault();
        console.log(picture);
        //if (picture.id == 1) {
            picture.is_liked = true;
       // }

        /*this.dataService.likePicture(pictureId)
            .subscribe((response: boolean) => {
                console.log(response);
                if (response.response) {
                    console.log(event);
                    this._isLiked = true;
                }
            });*/
    }
}