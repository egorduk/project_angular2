import { Component } from '@angular/core';
import { DataService } from '../common/service/data.service';
import { Router } from '@angular/router';

@Component({
    selector: 'home',
    styleUrls: ['app/home/style.css'],
    templateUrl: 'app/home/home.component.html'
})

export class HomeComponent {

    constructor(private router: Router, private dataService: DataService) {
    }

    logout() {
        localStorage.removeItem('id_token');

        this.router.navigate(['/login']);
    }

    friends(event) {
        event.preventDefault();

        this.router.navigate(['/friends']);
    }
}
