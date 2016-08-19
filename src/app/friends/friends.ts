import { Component, Input, ViewChild } from '@angular/core';
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
import { FocusDirective } from '../common/directive/focus.directive';

@Component({
    selector: 'friends',
    directives: [ ROUTER_DIRECTIVES, CORE_DIRECTIVES, FocusDirective ],
    styleUrls: ['app/friends/style.css'],
    templateUrl: 'app/friends/friends.html'
})

export class Friends {

    pictures: IPicture[];
    users: IUser[];
    comments: IComment[];
    _selectedPicture: string = '';
    //_isLiked: boolean = false;
    _openModalWindow: boolean = false;
    _setFocusCommentInput: boolean = false;
    //_isFollowed: boolean = true;
    _userId: number;

    constructor(public router: Router, public http: Http, private authHttp: AuthHttp, private dataService: DataService) {
        let token = localStorage.getItem('id_token');
        let data = window.jwt_decode(token);
        this._userId = data.uid;
        this.getFriendsPictures();
        //console.log();
    }

    getFriendsPictures() {
        this.dataService.getFriendsPictures()
            .subscribe((pictures: IPicture[]) => {
                //console.log(pictures);
                this.pictures = (pictures.response) ? pictures.pictures : null;
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

    followUser(event, userId, mode) {
        event.preventDefault();
        //console.log(this._selectedPicture);
        this.dataService.followUser(userId)
            .subscribe((response: boolean) => {
                console.log(response);
                if (response.response) {
                    if (mode == 'feed') {
                        this.getFriendsPictures();
                    } else if (mode == 'picture') {
                        this._selectedPicture.is_followed = true;
                    }
                }
            });
    }

    closePopup() {
        this._openModalWindow = false;
        this._setFocusCommentInput = false;
        this.getFriendsPictures();
        this.getUnfollowUsers();
    }

    openPopup(picture) {
        this._selectedPicture = picture;
        this._selectedPicture.is_followed = true;
        this.getPictureComments(this._selectedPicture);

        if (this._selectedPicture.tags) {
            this._selectedPicture.tags = this._selectedPicture.tags.split(',');
        }

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

    deleteComment(event, comment, picture) {
        event.preventDefault();

        this.dataService.deletePictureComment(comment.comment_id)
            .subscribe((response: boolean) => {
                if (response.response) {
                    this.getPictureComments(picture);
                }
            });
    }

    unfollowUser(event, userId) {
        event.preventDefault();
        console.log(this._selectedPicture);

        this.dataService.unfollowUser(userId)
            .subscribe((response: boolean) => {
                if (response.response) {
                    this._selectedPicture.is_followed = false;
                }
            });
    }

    getNextPicture(event, picture) {
        event.preventDefault();
    }
}