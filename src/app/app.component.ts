import { Component } from '@angular/core';
import { Router, ROUTER_DIRECTIVES } from '@angular/router';
import { APP_PROVIDERS } from './app.providers';
//import {LoggedInRouterOutlet} from './LoggedInOutlet';

@Component({ 
  moduleId: module.id,
  selector: 'app-container',
  template: `<router-outlet></router-outlet>`,
  directives: [ ROUTER_DIRECTIVES/*, LoggedInRouterOutlet*/ ],
  providers: [ APP_PROVIDERS ]
})

export class AppComponent {
    constructor(public router: Router) {}
}
