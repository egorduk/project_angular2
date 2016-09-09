import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { DataService } from '../common/service/data.service';
import { IPicture, IUser, IComment, IGallery } from '../common/interfaces';

@Component({
    selector: 'friends',
    styleUrls: ['app/friends/style.css'],
    templateUrl: 'app/friends/friends.component.html'
})

export class FriendsComponent {

    private pictures: IPicture[] = [];
    private users: IUser[] = [];
    private comments: IComment[] = [];
    private galleries: IGallery[] = [];

    private _selectedPicture: string = '';
    private _openModalWindow: boolean = false;
    private _setFocusCommentInput: boolean = false;
    private _currentUserId: number = 0;

    private warningNoPicturesAlert: Object =  {
        type: 'warning',
        msg: 'There is no pictures yet',
        is_show: false
    };

    private warningNoFollowersAlert: Object =  {
        type: 'warning',
        msg: 'There is no followers',
        is_show: false
    };

    constructor(private router: Router, private dataService: DataService) {
        let token = localStorage.getItem('id_token');
        let data = window.jwt_decode(token);
        this._currentUserId = data.uid;

        this.getFriendsPictures();
    }

    getFriendsPictures() {
        this.dataService.getFriendsPictures(this._currentUserId)
            .subscribe((pictures: IPicture[]) => {
                if (pictures.response) {
                    this.pictures = pictures.pictures;

                    if (this.pictures) {
                        this.pictures.forEach((value: any, key: any) => {
                            if (value.gallery_ids) {
                                this.pictures[key].gallery_ids = value.gallery_ids.split(',');
                            }
                        });

                        this.warningNoPicturesAlert.is_show = false;
                    }
                } else {
                    this.pictures = null;
                    this.warningNoPicturesAlert.is_show = true;
                }
            });

        this.getUnfollowUsers();
        this.getUserGallery();
    }

    getUnfollowUsers() {
        this.dataService.getUnfollowUsers(this._currentUserId)
            .subscribe((users: IUser[]) => {
                //console.log(users);
                if (users.response) {
                    users.users.forEach((value: any, key: any) => {
                        users.users[key].pictures = value.pictures.split(',', 3);
                    });

                    this.users = users.users;
                } else {
                    this.users = null;
                    this.warningNoFollowersAlert.is_show = true;
                }
            });
    }

    followUser(event, user, mode) {
        event.preventDefault();

        this.dataService.followUser(user.id)
            .subscribe((response: boolean) => {
                if (response.response) {
                    if (mode == 'feed') {
                        this.getFriendsPictures();
                    } else if (mode == 'picture') {
                        this._selectedPicture.is_followed = true;
                    }

                    //console.log(this.users);
                    //let ind = this.users.findIndex(this.test(user));

                  /*  let res = this.users.filter(function(u) {
                        return user === u; // Filter out the appropriate one
                    });
                    //console.log(res);
                   // console.log(ind);
                    //this.users.rem(user);
                    /*let ind = this.users.findIndex(user);
                    console.log(ind);
                    this.users.splice(ind, 1);
                    console.log(this.users);*/
                }
            });
    }

    closePopup() {
        this._openModalWindow = false;
        this._setFocusCommentInput = false;
        this.getFriendsPictures();
    }

    openPopup(picture) {
        this._selectedPicture = picture;
        this._selectedPicture.is_followed = true;
        this.getPictureComments(this._selectedPicture);
        this.preparePictureTags();
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

        this.dataService.deletePictureComment(picture.picture_id, comment.comment_id)
            .subscribe((response: boolean) => {
                if (response.response) {
                    this.getPictureComments(picture);
                }
            });
    }

    unfollowUser(event, picture) {
        event.preventDefault();

        this.dataService.unfollowUser(picture.user_id)
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
        this._selectedPicture.is_followed = true;
        this.getPictureComments(this._selectedPicture);
        this.preparePictureTags();
    }

    getPrevPicture(event, picture) {
        event.preventDefault();

        let index = this.getSelectedPictureIndex(picture);

        if (index == 0) {
            index = this.pictures.length;
        }

        this._selectedPicture = this.pictures[index - 1];
        this._selectedPicture.is_followed = true;
        this.getPictureComments(this._selectedPicture);
        this.preparePictureTags();
    }

    getSelectedPictureIndex(picture) {
        let filteredPicture = this.pictures.filter((pic) => pic === picture);

        return this.pictures.indexOf(filteredPicture[0]);
    }

    preparePictureTags() {
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
        this.dataService.getUserGalleries(this._currentUserId)
            .subscribe((galleries: IGallery[]) => {
                if (galleries.response) {
                    if (galleries.galleries) {
                        galleries.galleries.forEach((value: any, key: any) => {
                            if (typeof value.picture_ids === 'string') {
                                galleries.galleries[key].picture_ids = value.picture_ids.split(',');
                            }
                        });
                    }

                    this.galleries = galleries.galleries;
                } else {
                    this.galleries = null;
                }
            });
    }

    addPictureToGallery(event, picture, gallery) {
        event.preventDefault();

        this.dataService.addPictureToGallery(gallery.gallery_id, picture.picture_id)
            .subscribe((response: boolean) => {
                if (response.response) {
                    this.getUserGallery();
                }
            });
    }
}