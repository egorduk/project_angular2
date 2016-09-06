import { Component, OnInit, ElementRef, ChangeDetectionStrategy } from '@angular/core';
import { Router } from '@angular/router';
import { Http } from '@angular/http';

import { DataService } from '../common/service/data.service';

declare  var $:any;

@Component({
    selector: 'gallery',
    styleUrls: ['app/gallery/style.css'],
    templateUrl: 'app/gallery/gallery.component.html'
})

export class GalleryComponent implements OnInit {
    //private sub: Subscription;
    private _userLogin: string = '';
    private _currentUserId: number = 0;
    private _isCurrentUser: boolean = false;
    private user: IUser;
    private el: HTMLElement;
    private _token: string = '';
    private galleries: IGallery[] = [];

    public status:{isopen:boolean} = {isopen: false};


    constructor(private router: Router, private http: Http, private dataService: DataService, private el: ElementRef) {
        this.el = el.nativeElement;
        this._token = localStorage.getItem('id_token');
        let data = window.jwt_decode(this._token);
        this._currentUserId = data.uid;
       // this.router.navigate(['/hero', hero.id]);
        //this.getUserGalleries(6);
    }

    ngOnInit() {
        //this._userLogin = this.router.url.split('/')[2];
        //this.getUserInfo();
    }

    ngAfterViewChecked() {
        /*let imgs = $(this.el).find('#tiles li');

        let options = {
            autoResize: true, // This will auto-update the layout when the browser window is resized.
            container: $('#main'), // Optional, used for some extra CSS styling
            offset: 5, // Optional, the distance between grid items
            itemWidth: 450 // Optional, the width of a grid item
        };

        imgs.wookmark(options);*/
    }

    selectTab(event) {
        let activeTab = event.heading;
        console.log(activeTab);

        if (activeTab == 'Pictires') {

        } else if (activeTab == 'Galleries') {

        } else {

        }
    }

    getUserGalleries(userId) {
        this.dataService.getUserGalleries(userId)
            .subscribe((galleries: IGallery[]) => {
                if (galleries.response) {
                    console.log(galleries);
                    this.galleries = galleries.galleries;
                    /*if (user.user) {
                        this.user = user.user;

                        if (this.user.id == this._currentUserId) {
                            this._isCurrentUser = true;
                        }

                        //console.log(this.user.id);
                        this.getUserPictures(this.user.id);
                    }*/
                } else {
                    this.router.navigate(['/home']);
                }
            });
    }
}