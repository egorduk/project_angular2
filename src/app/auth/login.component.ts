import { Component, OnInit } from '@angular/core';
import { Router,  ActivatedRoute } from '@angular/router';
import { DataService } from '../shared/services/data.service';
import { IUser } from '../shared/interfaces';
//import { UserService } from '../shared/services/user.service';

@Component({
    moduleId: module.id,
    selector: 'login',
    templateUrl: 'login.component.html'
})

export class LoginComponent implements OnInit {

    user: IUser =
    {
        id: 0,
        firstName: '',
        lastName: '',
        login: '',
        email: '',
        password: ''
    };

    _hideError: boolean = true;

    constructor(private router: Router,
                private route: ActivatedRoute,
                private dataService: DataService,
                /*private authService: UserService*/) { }

    ngOnInit() {
    }

    onSignIn(event: Event) {
        event.preventDefault();
        //console.log(this.user);
        this.dataService.loginUser(this.user)
            .subscribe((response: boolean) => {
                this._hideError = response;
                console.log(response);
                //this.router.navigate(['Home']);
                //this.router.navigate(['/']);
            });
    }

    onSignUp(event: Event) {
        /*event.preventDefault();
         this.router.navigate(['/']);*/
    }

}