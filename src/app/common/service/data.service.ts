import { Injectable } from '@angular/core';
import { Http, Response, HTTP_PROVIDERS } from '@angular/http';
import { Observable } from 'rxjs/Observable';
import {Observer} from 'rxjs/Observer';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import { AuthHttp } from 'angular2-jwt/angular2-jwt';
import { contentHeaders } from '../headers';
import { IPicture, IUser, IGallery, IComment, ITag } from '../interfaces';
import { GlobalService } from './global.service';

@Injectable()
export class DataService {

    _baseUrl: string = '';
    _apiUrl: string = '';
    pictures: IPicture[];
    users: IUser[];
    comments: IComment[];
    galleries: IGallery[];

    tasks: Array<IPicture>;

    constructor(private http: Http, private authHttp: AuthHttp, private globalService: GlobalService) {
        this._apiUrl = this.globalService.getApiUrl();
    }

    getFriendsPictures() : Observable<IPicture[]> {
        return this.authHttp.get(this._apiUrl + '/get_friends_pictures')
            .map((response: Response) => {
                this.pictures = response.json();
                //console.log(this.pictures);
                return this.pictures;
            })
            .catch(this.handleError);
    }

    getUnfollowUsers() : Observable<IUser[]> {
        return this.authHttp.get(this._apiUrl + '/get_unfollow_users')
            .map((response: Response) => {
                this.users = response.json();
                //console.log(this.users);
                return this.users;
            })
            .catch(this.handleError);
    }

    followUser(id: number) : Observable<boolean> {
        let body = JSON.stringify({ id });
        return this.authHttp.post(this._apiUrl + '/follow_user', body, { headers: contentHeaders })
            .map((response: Response) => {
                return response.json();
            })
            .catch(this.handleError);
    }

    getUnfollowUser() : Observable<IUser> {
        return this.authHttp.get(this._apiUrl + '/get_unfollow_users')
            .map((response: Response) => {
                this.users = response.json();
                //console.log(this.users);
                return this.users;
            })
            .catch(this.handleError);
    }

    likePicture(pictureId: number) : Observable<boolean> {
        let body = JSON.stringify({ pictureId });

        return this.authHttp.post(this._apiUrl + '/likes', body, { headers: contentHeaders })
            .map((response: Response) => {
                return response.json();
            })
            .catch(this.handleError);
    }

    unlikePicture(pictureId: number) : Observable<boolean> {
        return this.authHttp.delete(this._apiUrl + '/likes/' + pictureId, { headers: contentHeaders })
            .map((response: Response) => {
                return response.json();
            })
            .catch(this.handleError);
    }

    getPictureComments(pictureId: number) : Observable<IComment[]> {
        return this.authHttp.get(this._apiUrl + '/comments/' + pictureId)
            .map((response: Response) => {
                this.comments = response.json();
                //console.log(this.users);
                return this.comments;
            })
            .catch(this.handleError);
    }

    addPictureComment(comment: string, pictureId: number) : Observable<boolean> {
        let body = JSON.stringify({ comment, pictureId });

        return this.authHttp.post(this._apiUrl + '/comments', body, { headers: contentHeaders })
            .map((response: Response) => {
                return response.json();
            })
            .catch(this.handleError);
    }

    addUserGallery(gallery: string, pictureId: number) : Observable<IGallery[]> {
        let body = JSON.stringify({ gallery, pictureId });

        return this.authHttp.post(this._apiUrl + '/galleries', body, { headers: contentHeaders })
            .map((response: Response) => {
                return response.json();
            })
            .catch(this.handleError);
    }

    addPictureInGallery(galleryId: number, pictureId: number) : Observable<boolean> {
        let body = JSON.stringify({ galleryId, pictureId });

        return this.authHttp.post(this._apiUrl + '/galleries/pictures', body, { headers: contentHeaders })
            .map((response: Response) => {
                return response.json();
            })
            .catch(this.handleError);
    }

    deletePictureComment(commentId: number) : Observable<boolean> {
        return this.authHttp.delete(this._apiUrl + '/comments/' + commentId, { headers: contentHeaders })
            .map((response: Response) => {
                return response.json();
            })
            .catch(this.handleError);
    }

    unfollowUser(userId: number) : Observable<boolean> {
        return this.authHttp.delete(this._apiUrl + '/users/' + userId, { headers: contentHeaders })
            .map((response: Response) => {
                return response.json();
            })
            .catch(this.handleError);
    }

    getUserGalleriesWithCheckedPictures() : Observable<IGallery[]> {
        return this.authHttp.get(this._apiUrl + '/galleries/users')
            .map((response: Response) => {
                this.galleries = response.json();
                //console.log(this.users);
                return this.galleries;
            })
            .catch(this.handleError);
    }

    getUserInfo(login: string) : Observable<IUser[]> {
        return this.authHttp.get(this._apiUrl + '/users/login/' + login)
            .map((response: Response) => {
                return response.json();
            })
            .catch(this.handleError);
    }

    getUserPictures(userId: number) : Observable<IPicture[]> {
        return this.authHttp.get(this._apiUrl + '/pictures/users/' + userId)
            .map((response: Response) => {
                this.pictures = response.json();
                return this.pictures;
            })
            .catch(this.handleError);

        /*return this.http.get('pictures.json')
            *//*.map((res: Response) => {
                this.pictures = res.json();
                console.log('res.json()', res.json());
                console.log('this.pictures', this.pictures);
                return this.pictures;
            })*//*
            .map( (responseData) => {
                return responseData.json();
            })
            // next transform - each element in the
            // array to a Task class instance
            .map((tasks: Array<any>) => {
                let result:Array<IPicture> = [];
                if (tasks) {
                    tasks.forEach((task) => {
                        result.push(new IPicture(task.id, task.name));
                    });
                }
                console.log(result);
                return result;
            });*/
            // subscribe to output from this observable and bind
            // the output to the component when received
            //.subscribe( res => this.tasks = res);

            //.catch(this.handleError);
    }

    getTags() : Observable<ITags[]> {
        return this.authHttp.get(this._apiUrl + '/tags/')
            .map((response: Response) => {
                return response.json();
            })
            .catch(this.handleError);
    }

    private handleError(error: any) {
        console.error(error);
        return Observable.throw(error.json().error || 'Server error');
    }
}