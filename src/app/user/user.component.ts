import { Component, OnInit, ElementRef } from '@angular/core';
import { Router } from '@angular/router';
import { DataService } from '../common/service/data.service';

declare var $:any;

@Component({
    selector: 'user',
    styleUrls: ['app/user/style.css'],
    templateUrl: 'app/user/user.component.html'
})

export class UserComponent implements OnInit {

    private _userLogin: string = '';
    private _currentUserId: number = 0;
    private _isCurrentUser: boolean = false;
    private _isViewGallery: boolean = false;
    //pictures: IPicture[] = [];
    pictures: Array<IPicture> = [];
    private el: HTMLElement;
    private _token: string = '';
    private _selectedPicture: IPicture;
    private _modalPopup: any;
    private _tabsMode: string = '';

    private galleries: IGallery[] = [];
    private gallery: IGallery[];
    private user: IUser;

    private warningNoPicturesAlert: Object =  {
        type: 'warning',
        msg: 'There is no pictures yet',
        is_show: false
    };

    private warningNoGalleriesAlert: Object =  {
        type: 'warning',
        msg: 'There is no galleries yet',
        is_show: false
    };

    private successUpdateProfileAlert: Object =  {
        type: 'success',
        msg: 'Your profile was updated',
        is_show: false
    };


    constructor(private router: Router, private dataService: DataService, private el: ElementRef) {
        this.el = el.nativeElement;
        this._token = localStorage.getItem('id_token');
        let data = window.jwt_decode(this._token);
        this._currentUserId = data.uid;
    }

    onEmitter():void {
        this.getUserPictures(this.user.id);
    }

    ngOnInit() {
        this._userLogin = this.router.url.split('/')[2];
        this.getUserInfo(this._userLogin);
    }

    ngAfterViewChecked() {
        let imgs = $(this.el).find('#tiles li');

        let options = {
            autoResize: true, // This will auto-update the layout when the browser window is resized.
            container: $('#main'), // Optional, used for some extra CSS styling
            offset: 5, // Optional, the distance between grid items
            itemWidth: 450 // Optional, the width of a grid item
        };

        imgs.wookmark(options);
    }

    private getUserInfo(login) {
        this.dataService.getUserInfoByLogin(login)
            .subscribe((user: IUser) => {
                if (user.response) {
                    if (user.user) {
                        this.user = user.user;

                        if (this.user.id == this._currentUserId) {
                            this._isCurrentUser = true;
                        }

                        this.getUserPictures(this.user.id);
                    }
                } else {
                    this.router.navigate(['/friends']);
                }
            });
    }

    private getUserPictures(userId) {
        this.dataService.getUserPictures(userId)
            .subscribe((pictures: IPicture[]) => {
                if (pictures.response) {
                    this.pictures = pictures.pictures;
                    this.warningNoPicturesAlert.is_show = false;
                } else {
                    this.pictures = null;
                    this.warningNoPicturesAlert.is_show = true;
                }
            });
    }

    private preparePictureTags() {
        if (typeof this._selectedPicture.tags === 'string') {
            this._selectedPicture.tags = this._selectedPicture.tags.split(',');
        }
    }

    private likePicture(event, picture) {
        event.preventDefault();

        if (picture.is_liked == '1') {
            this.dataService.unlikePicture(picture.picture_id)
                .subscribe((response: boolean) => {
                    if (response.response) {
                        picture.is_liked = '0';
                    }
                });
        } else {
            this.dataService.likePicture(picture.picture_id)
                .subscribe((response: boolean) => {
                    if (response.response) {
                        picture.is_liked = '1';
                    }
                });
        }
    }

    private deletePicture(picture) {
        let confirmAnswer = confirm("Are you sure?");

        if (confirmAnswer) {
            this.dataService.deletePicture(picture.picture_id)
                .subscribe((response: boolean) => {
                    if (response.response) {
                        this.getUserPictures(this.user.id);
                    }
                });
        }
    }

    private actionOnOpen() {
    }

    private actionOnClose() {
        this._selectedPicture = null;
    }

    /*public actionOnSubmit(editPictureForm) {
        console.log(editPictureForm);
        console.log(this._selectedPicture);
    }*/

    private openEditModalPopup(picture, modalPopup) {
        this._selectedPicture = picture;
        this._modalPopup = modalPopup;
        this.preparePictureTags();
        this._modalPopup.open();
    }

    private onSubmit() {
        this.updatePictureName();
    }

    private onSubmitProfileForm() {
        this.updateUserInfo();
    }

    private updatePictureName() {
        console.log(this._selectedPicture);

        this.dataService.updatePictureName(this._selectedPicture.picture_id, this._selectedPicture.name)
            .subscribe((response: boolean) => {
                if (response.response) {
                    this._modalPopup.close();
                }
            });
    }

    private selectTab(event) {
        let activeTab = event.heading;

        if (activeTab == 'Pictures') {
            this._tabsMode = 'pictures';
        } else if (activeTab == 'Galleries') {
            if (this._isViewGallery) {
                this._isViewGallery = false;
            }

            this._tabsMode = 'galleries';
            this.getUserGalleries();
        } else {
            this._tabsMode = 'my_info';
        }
    }

    private getUserGalleries() {
        this.galleries = null;

        this.dataService.getUserGalleries(this.user.id)
            .subscribe((galleries: IGallery[]) => {
                if (galleries.response) {
                    if (galleries.galleries) {
                        galleries.galleries.forEach((value: any, key: any) => {
                            if (typeof value.pictures === 'string') {
                                let pictures = value.pictures.split(',');
                                galleries.galleries[key].cover_picture = pictures[Math.floor(Math.random() * pictures.length)];
                            } else {
                                galleries.galleries[key].cover_picture = 'cover_default.png';
                            }
                        });
                    }

                    this.galleries = galleries.galleries;
                    this.warningNoGalleriesAlert.is_show = false;
                } else {
                    this.warningNoGalleriesAlert.is_show = true;
                }
            });
    }

    private viewGallery(event, gallery) {
        event.preventDefault();

        if (gallery.cnt_pictures > 0) {
            this.dataService.getGalleryPictures(gallery.gallery_id)
                .subscribe((pictures: IPicture[]) => {
                    if (pictures.response) {
                        this.gallery = pictures;
                        this._isViewGallery = true;
                        this.ngAfterViewChecked();
                    } else {
                        this.gallery = null;
                        this._isViewGallery = false;
                    }
                });
        }
    }

    private deleteGallery(gallery) {
        let confirmAnswer = confirm("Are you sure?");

        if (confirmAnswer) {
            this.dataService.deleteGallery(gallery.gallery_id)
                .subscribe((response: boolean) => {
                    if (response.response) {
                        this.getUserGalleries();
                    }
                });
        }
    }

    private updateUserInfo() {
        this.dataService.updateUserInfo(this.user)
            .subscribe((response: boolean) => {
                if (response.response) {
                    this.getUserInfo(this.user.login);
                    this.successUpdateProfileAlert.is_show = true;
                } else {
                    this.successUpdateProfileAlert.is_show = false;
                }
            });
    }
}