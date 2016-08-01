import { Component, OnInit } from '@angular/core';
import { Router,  ActivatedRoute } from '@angular/router';
import { DataService } from '../shared/services/data.service';
import { IUser } from '../shared/interfaces';

@Component({ 
  moduleId: module.id,
  selector: 'login',
  templateUrl: 'login.component.html',
  //directives: [ROUTER_DIRECTIVES]
})

export class LoginComponent implements OnInit {

    user: IUser =
    {
        id: 0,
        firstName: '',
        lastName: '',
        login: '',
        password: ''
    };

    constructor(private router: Router,
                private route: ActivatedRoute,
                private dataService: DataService) { }

    ngOnInit() {
    }

    onSignIn(event: Event) {
        event.preventDefault();
        //console.log(this.user);
        this.dataService.loginUser(this.user)
            .subscribe((status: boolean) => {
                alert(status);
                //this.router.navigate(['/']);
            });
    }

    onSignUp(event: Event) {
        /*event.preventDefault();
        this.router.navigate(['/']);*/
    }

}