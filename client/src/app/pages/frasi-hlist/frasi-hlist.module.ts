import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FrasiHListComponent } from './frasi-hlist.component';
import { FrasiHListRoutingModule } from './frasi-hlist-routing.module';
import { FormsModule } from '@angular/forms';
import { SharedModule } from '../../shared/shared.module';


@NgModule({
  imports: [
    CommonModule,
    FrasiHListRoutingModule,
    FormsModule,
    SharedModule
  ],
  declarations: [
    FrasiHListComponent
  ]
})
export class FrasiHListModule { }
