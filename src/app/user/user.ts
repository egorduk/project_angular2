import { Component, OnInit, ElementRef } from '@angular/core';
import { Router, ROUTER_DIRECTIVES } from '@angular/router';
import { CORE_DIRECTIVES } from '@angular/common';
import { Http } from '@angular/http';
import { Observable } from 'rxjs/Observable';
import {Observer} from 'rxjs/Observer';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import { DataService } from '../common/service/data.service';
import { ChildComponent } from '../common/service/child.component';
import { IUser, IPicture, IGallery } from '../common/interfaces';
import { SafeBgPipe } from '../common/pipe/safe.pipe';
import { HeaderComponent } from '../header/header.component';

declare  var $:any;

const URL = 'http://localhost:80/project_angular2/api/pictures';

@Component({
    selector: 'user',
    directives: [ ROUTER_DIRECTIVES, CORE_DIRECTIVES, HeaderComponent ],
    pipes: [ SafeBgPipe ],
    styleUrls: ['app/user/style.css'],
    templateUrl: 'app/user/user.html'
})

export class User implements OnInit {
    //private sub: Subscription;
    private _userLogin: string = '';
    private _userId: number = 0;
    private _isCurrentUser: boolean = false;
    private user: IUser;
    //pictures: IPicture[] = [];
   // pictures: Observable<IPicture> = [];
    pictures: Array<IPicture> = [];
    private el: HTMLElement;
    private _token: string = '';
    private galleries: IGallery[] = [];

    constructor(private router: Router, private http: Http, private dataService: DataService, private el: ElementRef) {
        this.el = el.nativeElement;
        this._token = localStorage.getItem('id_token');
        let data = window.jwt_decode(this._token);
        this._userId = data.uid;
       // this.router.navigate(['/hero', hero.id]);
    }

    onEmitter():void {
        this.getUserPictures(this._userId);
    }

    ngOnInit() {
        this._userLogin = this.router.url.split('/')[2];
        this.getUserInfo();
    }

    ngAfterViewChecked() {
        let imgs = $(this.el).find('#tiles li');
        //console.log(imgs);

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

                        if (this.user.id == this._userId) {
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
                //this.pictures = pictures.pictures;
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

        //this.test();
        //console.log('this.pictures', this);

    }

    getUserGallery() {
        this.galleries = null;

        this.dataService.getUserGalleriesWithCheckedPictures()
            .subscribe((galleries: IGallery[]) => {
                if (galleries.response) {
                    if (galleries.galleries) {
                        galleries.galleries.forEach((value: any, key: any) => {
                            if (typeof value.picture_ids === 'string') {
                                galleries.galleries[key].picture_ids = value.picture_ids.split(',');
                            }
                        });
                    } else {

                    }

                    this.galleries = galleries.galleries;
                }

                //console.log('this.galleries', this.galleries);
            });
    }
}