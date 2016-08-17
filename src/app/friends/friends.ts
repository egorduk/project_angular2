import { Component, Input, ElementRef, Directive, Injectable } from '@angular/core';
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

@Directive({
    selector: '[focus]'
})
class FocusDirective {
@Input()
    focus: boolean;

    constructor(private element: ElementRef) {}

    protected ngOnChanges() {
        console.log(this.focus);
        //(this.focus) ? this.element.nativeElement.focus() : this.element.nativeElement.blur();
        if (this.focus) {
            this.element.nativeElement.focus();
        }
    }
}

@Component({
    selector: 'friends',
    directives: [ ROUTER_DIRECTIVES, CORE_DIRECTIVES, FocusDirective ],
    templateUrl: 'app/friends/friends.html'
})

export class Friends {

    pictures: IPicture[];
    users: IUser[];
    comments: IComment[];
    _selectedPicture: string = '';
    _isLiked: boolean = false;
    _openModalWindow: boolean = false;
    _setFocusCommentInput: boolean = false;

    constructor(public router: Router, public http: Http, private authHttp: AuthHttp, private dataService: DataService) {
        //this.jwt = localStorage.getItem('id_token');
        this.getFriendsPictures();
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
        this.users = null;

        this.dataService.getUnfollowUsers()
            .subscribe((users: IUser[]) => {
                //console.log(users);
                if (users.response) {
                    users.users.forEach((value: any, key: any) => {
                        users.users[key].pictures = value.pictures.split(',', 3);
                    });
                    this.users = users.users;
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
        this._openModalWindow = false;
        this._setFocusCommentInput = false;
    }

    openPopup(picture) {
        this._selectedPicture = picture;
        this.getPictureComments(this._selectedPicture);
        this._openModalWindow = true;
    }

    openPopupAddComment(picture) {
        this.openPopup(picture);
        this._setFocusCommentInput = true;
    }

    likePicture(event, picture) {
        event.preventDefault();

        if (picture.is_liked == '1') {
            this.dataService.unlikePicture(picture.picture_id)
                .subscribe((response: boolean) => {
                    console.log(response);
                    if (response.response) {
                        if (picture.cnt_like != '0') {
                            picture.cnt_like--;
                        }
                        picture.is_liked = '0';
                    }
                });
        } else {
            this.dataService.likePicture(picture.picture_id)
                .subscribe((response: boolean) => {
                    console.log(response);
                    if (response.response) {
                        picture.is_liked = '1';
                        picture.cnt_like++;
                    }
                });
        }
    }

    getPictureComments(picture) {
        this.comments = null;

        this.dataService.getPictureComments(picture.picture_id)
            .subscribe((comments: IComment[]) => {
                if (comments.response) {
                    this.comments = comments.comments;
                }
            });
    }

    addComment(picture, elInputComment) {
        let comment = elInputComment.value;

        if (comment) {
            this.dataService.addPictureComment(comment, picture.picture_id)
                .subscribe((response: boolean) => {
                    if (response.response) {
                        elInputComment.value = '';
                        this.getPictureComments(picture);
                    }
                });
        }
    }
}