import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SostanzaListComponent } from './sostanza-list.component';
import { SostanzaListRoutingModule } from './sostanza-list-routing.module';
import { FormsModule } from '@angular/forms';
import { SharedModule } from '../../shared/shared.module';


@NgModule({
  imports: [
    CommonModule,
    SostanzaListRoutingModule,
    FormsModule,
    SharedModule
  ],
  declarations: [
    SostanzaListComponent
  ]
})
export class SostanzaListModule { }
