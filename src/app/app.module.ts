import { NgModule }      from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FormsModule } from '@angular/forms';
import { HttpModule } from '@angular/http';

import { routing } from './app.routes';

import { AUTH_PROVIDERS } from 'angular2-jwt/angular2-jwt';

import { DataService } from './common/service/data.service';
import { SafeBgPipe } from './common/pipe/safe.pipe';

import { AppComponent }  from './app.component';
import { AlertComponent } from 'ng2-bootstrap/components/alert';
import { DropdownModule  } from 'ng2-bootstrap/components/dropdown';
import { SELECT_DIRECTIVES } from 'ng2-select/ng2-select';
import { ModalModule } from "ng2-modal";

@NgModule({
    imports: [ BrowserModule, FormsModule, HttpModule, routing, DropdownModule, ModalModule ],       // module dependencies
    declarations: [ AppComponent, AlertComponent, SafeBgPipe, SELECT_DIRECTIVES ],   // components and directives
    bootstrap: [ AppComponent ],     // root component
    providers: [ AUTH_PROVIDERS, DataService ]                    // services
})
export class AppModule { }