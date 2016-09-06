import { Component, OnInit, ElementRef, ViewContainerRef, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Http } from '@angular/http';

import { DataService } from '../common/service/data.service';

declare  var $:any;

const URL = 'http://localhost:80/project_angular2/api/pictures';

@Component({
    selector: 'user',
    //directives: [ ROUTER_DIRECTIVES, CORE_DIRECTIVES, HeaderComponent, AlertComponent/*, DropdownModule*/ ],
    //pipes: [ SafeBgPipe ],
    styleUrls: ['app/user/style.css'],
    templateUrl: 'app/user/user.html'
})

export class User implements OnInit {
    //private sub: Subscription;
    private _userLogin: string = '';
    private _currentUserId: number = 0;
    private _isCurrentUser: boolean = false;
    private user: IUser;
    //pictures: IPicture[] = [];
   // pictures: Observable<IPicture> = [];
    pictures: Array<IPicture> = [];
    private el: HTMLElement;
    private _token: string = '';
    private galleries: IGallery[] = [];
    private _isHideDropDown: boolean = false;
    private _selectedPicture: IPicture;
    private _modalPopup: any;

    public status:{isopen:boolean} = {isopen: false};


    constructor(private router: Router, private http: Http, private dataService: DataService, private el: ElementRef) {
        this.el = el.nativeElement;
        this._token = localStorage.getItem('id_token');
        let data = window.jwt_decode(this._token);
        this._currentUserId = data.uid;

        this.viewContainerRef = viewContainerRef;
       // this.router.navigate(['/hero', hero.id]);
    }

     @ViewChild('childModal') public childModal:ModalDirective;

    public showChildModal():void {
        this.childModal.show();
    }

    public hideChildModal():void {
        this.childModal.hide();
    }

    onEmitter():void {
        this.getUserPictures(this.user.id);
    }

    ngOnInit() {
        this._userLogin = this.router.url.split('/')[2];
        this.getUserInfo();
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

    getUserInfo() {
        this.dataService.getUserInfo(this._userLogin)
            .subscribe((user: IUser) => {
                if (user.response) {
                    //console.log(user);
                    if (user.user) {
                        this.user = user.user;

                        if (this.user.id == this._currentUserId) {
                            this._isCurrentUser = true;
                        }

                        //console.log(this.user.id);
                        this.getUserPictures(this.user.id);
                    }
                } else {
                    this.router.navigate(['/home']);
                }
            });

        //console.log(this.user);
    }

    getUserPictures(userId) {
        this.dataService.getUserPictures(userId)
            .subscribe((pictures: IPicture[]) => {
                this.pictures = (pictures.response) ? pictures.pictures : null;
                //console.log('this.pictures', this.pictures);
            });
            /*.subscribe(
           *//* data => {
                // Set the products Array
                this.pictures = data;
                console.log('this.pictures', this.pictures);
                console.log('data', data);
            }*//*
            res => this.pictures = res
            );*/
        //this.getPictureTags();
    }

    preparePictureTags() {
        //console.log(this._selectedPicture);
        if (typeof this._selectedPicture.tags === 'string') {
            this._selectedPicture.tags = this._selectedPicture.tags.split(',');
        }
    likePicture(event, picture) {
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

    deletePicture(picture) {
        this.dataService.deletePicture(picture.picture_id)
            .subscribe((response: boolean) => {
                if (response.response) {
                    this.getUserPictures(this.user.id);
                }
            });
    }

    /*public toggled(open:boolean):void {
        console.log('Dropdown is now: ', open);
    }

    public actionOnOpen() {
        //console.log('open');
    }

    public actionOnClose() {
        //console.log('close');
        this._selectedPicture = null;
    }

    /*public actionOnSubmit(editPictureForm) {
        console.log(editPictureForm);
        console.log(this._selectedPicture);
    }*/

    openModalPopup(picture, modalPopup) {
        this._selectedPicture = picture;
        this._modalPopup = modalPopup;

        //console.log(modalPopup);
        this.preparePictureTags();
        this._modalPopup.open();
    }

    onSubmit() {
        //this.submitted = true;
        this.updatePictureName();
    }

    updatePictureName() {
        console.log(this._selectedPicture);

        this.dataService.updatePictureName(this._selectedPicture.picture_id, this._selectedPicture.name)
            .subscribe((response: boolean) => {
                if (response.response) {
                    this._modalPopup.close();
                }
            });
    }

  /*  public alerts:Array<Object> = [
        {
            type: 'danger',
            msg: 'Oh snap! Change a few things up and try submitting again.'
        },
        {
            type: 'success',
            msg: 'Well done! You successfully read this important alert message.',
            closable: true
        }
    ];

    public closeAlert(i:number):void {
        this.alerts.splice(i, 1);
    }

    public addAlert():void {
        this.alerts.push({msg: 'Another alert!', type: 'warning', closable: true});
    }*/
}