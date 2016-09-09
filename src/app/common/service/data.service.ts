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
import { Headers } from '@angular/http';

@Injectable()
export class DataService {

    _baseUrl: string = '';
    _apiUrl: string = '';

    pictures: IPicture[];
    users: IUser[] = [];
    user: IUser[];
    comments: IComment[] = [];
    galleries: IGallery[] = [];

    //tasks: Array<IPicture>;

    constructor(private http: Http, private authHttp: AuthHttp, private globalService: GlobalService) {
        this._apiUrl = this.globalService.getApiUrl();
    }

    signup(email, password) : any {
        let body = JSON.stringify({ email, password });

        return this.http.post(this._apiUrl + '/unsecured/users', body, { headers: contentHeaders })
            .map((response: Response) => {
                return response.json();
            })
            .catch(this.handleError);
    }

    login(email, password) : Observable<IUser> {
        return this.http.get(this._apiUrl + '/unsecured/users/email/' + email + '/password/' + password)
            .map((response: Response) => {
                return response.json();
            })
            .catch(this.handleError);
    }

    getFriendsPictures(userId) : Observable<IPicture[]> {
        return this.authHttp.get(this._apiUrl + '/pictures/friends/users/' + userId)
            .map((response: Response) => {
                this.pictures = response.json();
                return this.pictures;
            })
            .catch(this.handleError);
    }

    getUnfollowUsers(userId) : Observable<IUser[]> {
        return this.authHttp.get(this._apiUrl + '/users/' + userId + '/unfollows')
            .map((response: Response) => {
                this.users = response.json();
                //console.log(this.users);
                return this.users;
            })
            .catch(this.handleError);
    }

    followUser(friendId: number) : Observable<boolean> {
        let body = JSON.stringify({ friendId });
        return this.authHttp.post(this._apiUrl + '/users/follows', body, { headers: contentHeaders })
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

        return this.authHttp.post(this._apiUrl + '/pictures/likes', body, { headers: contentHeaders })
            .map((response: Response) => {
                return response.json();
            })
            .catch(this.handleError);
    }

    unlikePicture(pictureId: number) : Observable<boolean> {
        return this.authHttp.delete(this._apiUrl + '/pictures/' + pictureId + '/likes')
            .map((response: Response) => {
                return response.json();
            })
            .catch(this.handleError);
    }

    getPictureComments(pictureId: number) : Observable<IComment[]> {
        return this.authHttp.get(this._apiUrl + '/pictures/' + pictureId + '/comments')
            .map((response: Response) => {
                this.comments = response.json();
                return this.comments;
            })
            .catch(this.handleError);
    }

    addPictureComment(comment: string, pictureId: number) : Observable<boolean> {
        let body = JSON.stringify({ comment });

        return this.authHttp.post(this._apiUrl + '/pictures/' + pictureId + '/comments', body, { headers: contentHeaders })
            .map((response: Response) => {
                return response.json();
            })
            .catch(this.handleError);
    }

    addUserGallery(galleryName: string, pictureId: number) : Observable<IGallery[]> {
        let body = JSON.stringify({ galleryName, pictureId });

        return this.authHttp.post(this._apiUrl + '/galleries', body, { headers: contentHeaders })
            .map((response: Response) => {
                return response.json();
            })
            .catch(this.handleError);
    }

    addPictureToGallery(galleryId: number, pictureId: number) : Observable<boolean> {
        let body = JSON.stringify({ galleryId, pictureId });

        return this.authHttp.post(this._apiUrl + '/galleries/pictures', body, { headers: contentHeaders })
            .map((response: Response) => {
                return response.json();
            })
            .catch(this.handleError);
    }

    deletePictureComment(commentId: number, pictureId: number) : Observable<boolean> {
        return this.authHttp.delete(this._apiUrl + '/pictures/' + pictureId + '/comments/' + commentId, { headers: contentHeaders })
            .map((response: Response) => {
                return response.json();
            })
            .catch(this.handleError);
    }

    unfollowUser(userId: number) : Observable<boolean> {
        return this.authHttp.delete(this._apiUrl + '/users/follows/' + userId, { headers: contentHeaders })
            .map((response: Response) => {
                return response.json();
            })
            .catch(this.handleError);
    }

    getUserGalleries(userId): Observable<IGallery[]> {
        return this.authHttp.get(this._apiUrl + '/galleries/users/' + userId)
            .map((response: Response) => {
                this.galleries = response.json();
                //console.log(this.galleries);
                return this.galleries;
            })
            .catch(this.handleError);
    }

    getUserInfoByLogin(login: string) : Observable<IUser[]> {
        return this.authHttp.get(this._apiUrl + '/users/login/' + login)
            .map((response: Response) => {
                this.user = response.json()
                return this.user;
            })
            .catch(this.handleError);
    }

    getUserInfoById(userId: number) : Observable<IUser[]> {
        return this.authHttp.get(this._apiUrl + '/users/id/' + userId)
            .map((response: Response) => {
                this.user = response.json()
                return this.user;
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

    getTags() : Observable<ITag[]> {
        return this.authHttp.get(this._apiUrl + '/tags/')
            .map((response: Response) => {
                return response.json();
            })
            .catch(this.handleError);
    }

    deletePicture(pictureId: number) : boolean {
        return this.authHttp.put(this._apiUrl + '/pictures/' + pictureId + '/status', '')
            .map((response: Response) => {
                //console.log(response);
                return response.json();
            })
            .catch(this.handleError);
    }

    deleteGallery(galleryId: number) : boolean {
        return this.authHttp.delete(this._apiUrl + '/galleries/' + galleryId)
            .map((response: Response) => {
                return response.json();
            })
            .catch(this.handleError);
    }

    updatePictureName(pictureId: number, pictureName: string) : boolean {
        let body = JSON.stringify({ pictureName });

        return this.authHttp.put(this._apiUrl + '/pictures/' + pictureId + '/name', body, { headers: contentHeaders })
            .map((response: Response) => {
                return response.json();
            })
            .catch(this.handleError);
    }

    getGalleryPictures(galleryId: number) : Observable<IPicture[]> {
        return this.authHttp.get(this._apiUrl + '/galleries/' + galleryId + '/pictures')
            .map((response: Response) => {
                this.pictures = response.json();
                return this.pictures;
            })
            .catch(this.handleError);
    }

    updateUserInfo(user: IUser) : Observable<boolean> {
        let login = user.login;
        let email = user.email;
        let info = user.info;
        let userId = user.id;

        let body = JSON.stringify({ login, email, info });

        return this.authHttp.put(this._apiUrl + '/users/' + userId, body, { headers: contentHeaders })
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