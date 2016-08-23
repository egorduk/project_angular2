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
import { IPicture, IUser, IComment, IGallery } from '../common/interfaces';
import { FocusDirective } from '../common/directive/focus.directive';
import { CheckGalleryDirective } from '../common/directive/checkGallery.directive';

@Component({
    selector: 'friends',
    directives: [ ROUTER_DIRECTIVES, CORE_DIRECTIVES, FocusDirective, CheckGalleryDirective ],
    styleUrls: ['app/friends/style.css'],
    templateUrl: 'app/friends/friends.html'
})

export class Friends {

    pictures: IPicture[];
    users: IUser[];
    comments: IComment[];
    galleries: IGallery[] = [];
    _selectedPicture: string = '';
    _openModalWindow: boolean = false;
    _setFocusCommentInput: boolean = false;
    _userId: number;
    _isChecked: boolean = false;

    constructor(public router: Router, public http: Http, private authHttp: AuthHttp, private dataService: DataService) {
        let token = localStorage.getItem('id_token');
        let data = window.jwt_decode(token);
        this._userId = data.uid;
        this.getFriendsPictures();
    }

    getFriendsPictures() {
        this.dataService.getFriendsPictures()
            .subscribe((pictures: IPicture[]) => {
                this.pictures = (pictures.response) ? pictures.pictures : null;

                if (this.pictures) {
                    this.pictures.forEach((value: any, key: any) => {
                        if (value.gallery_ids) {
                            this.pictures[key].gallery_ids = value.gallery_ids.split(',');
                        }
                    });
                }

                //console.log('this.pictures', this.pictures);
            });
        this.getUnfollowUsers();
        this.getUserGallery();
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
        this.getPictureTags();
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
        //console.log(this._selectedPicture);

        this.dataService.unfollowUser(userId)
            .subscribe((response: boolean) => {
                if (response.response) {
                    this._selectedPicture.is_followed = false;
                }
            });
    }

    getNextPicture(event, picture) {
        event.preventDefault();

        let index = this.getSelectedPictureIndex(picture);

        if (index == this.pictures.length - 1) {
            index = -1;
        }

        this._selectedPicture = this.pictures[index + 1];
        this.getPictureComments(this._selectedPicture);
        this.getPictureTags();
    }

    getPrevPicture(event, picture) {
        event.preventDefault();

        let index = this.getSelectedPictureIndex(picture);

        if (index == 0) {
            index = this.pictures.length - 1;
        }

        this._selectedPicture = this.pictures[index - 1];
        console.log(this._selectedPicture);
        this.getPictureComments(this._selectedPicture);
        this.getPictureTags();
    }

    getSelectedPictureIndex(picture) {
        let filteredPicture = this.pictures.filter((pic) => pic === picture);
        return this.pictures.indexOf(filteredPicture[0]);
    }

    getPictureTags() {
        //console.log(this._selectedPicture.tags);
        if (typeof this._selectedPicture.tags === 'string') {
            this._selectedPicture.tags = this._selectedPicture.tags.split(',');
        }
    }

    addUserGallery(event, elInputGallery, picture) {
        event.preventDefault();

        let galleryName = elInputGallery.value;

        if (galleryName) {
            this.dataService.addUserGallery(galleryName, picture.picture_id)
                .subscribe((response: IGallery[]) => {
                    if (response.response) {
                        this.getUserGallery();
                        elInputGallery.value = '';
                    }
                });
        }
    }

    getUserGallery() {
        this.galleries = null;

        this.dataService.getUserGalleriesWithCheckedPictures()
            .subscribe((galleries: IGallery[]) => {
                console.log('getUserGalleries', galleries);
                if (galleries.response) {
                    if (galleries.galleries) {
                        //if (typeof galleries.galleries.picture_ids === 'string') {
                            galleries.galleries.forEach((value: any, key: any) => {
                                galleries.galleries[key].picture_ids = value.picture_ids.split(',');
                            });
                       // }
                    } else {

                    }

                    this.galleries = galleries.galleries;
                }

                console.log('this.galleries', this.galleries);
            });
    }

    addPictureToGallery(event) {
    }

    test(event) {
       // console.log(event);

        if (event.value) {
            this._isChecked = true;
        } else {
            this._isChecked = false;
        }

        console.log(this._isChecked);
    }
}