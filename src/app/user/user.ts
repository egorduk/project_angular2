import { Component, OnInit, ElementRef } from '@angular/core';
import { Router, ROUTER_DIRECTIVES } from '@angular/router';
import { CORE_DIRECTIVES } from '@angular/common';
import { Http } from '@angular/http';
import { Observable } from 'rxjs/Observable';
import {Observer} from 'rxjs/Observer';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import { DataService } from '../common/service/data.service';
import { IUser, IPicture } from '../common/interfaces';
import { SafePipe } from '../common/pipe/safe.pipe';
import {FILE_UPLOAD_DIRECTIVES, FileUploader} from 'ng2-file-upload/ng2-file-upload';
//import { Subscription } from 'rxjs/Subscription';

declare  var $:any;

const URL = 'https://evening-anchorage-3159.herokuapp.com/api/';

@Component({
    selector: 'user',
    directives: [ ROUTER_DIRECTIVES, CORE_DIRECTIVES, FILE_UPLOAD_DIRECTIVES ],
    pipes: [ SafePipe ],
    styleUrls: ['app/user/style.css'],
    templateUrl: 'app/user/user.html'
})

export class User implements OnInit {

    //private sub: Subscription;
    private _userLogin: string = '';
    private _userId: number = 0;
    private _isCurrentUser: boolean = false;
    private user: IUser[];
    private pictures: IPicture[] = [];
    private el: HTMLElement;

    public uploader:FileUploader = new FileUploader({url: URL});
    public hasBaseDropZoneOver:boolean = false;
    public hasAnotherDropZoneOver:boolean = false;

    constructor(private router: Router, private http: Http, private dataService: DataService, private el: ElementRef) {
        this.el = el.nativeElement;

        let token = localStorage.getItem('id_token');
        let data = window.jwt_decode(token);
        this._userId = data.uid;
       // this.router.navigate(['/hero', hero.id]);

    }

    public fileOverBase(e:any):void {
        this.hasBaseDropZoneOver = e;
    }

    public fileOverAnother(e:any):void {
        this.hasAnotherDropZoneOver = e;
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
            .subscribe((user: IUser[]) => {
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

                if (this.pictures) {
                    /*this.pictures.forEach((value: any, key: any) => {
                        if (value.gallery_ids) {
                            this.pictures[key].gallery_ids = value.gallery_ids.split(',');
                        }
                    });*/
                }

                //console.log('this.pictures', this.pictures);
            });
    }
}