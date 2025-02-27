import { CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { NetComponent } from './net.component';

describe('NetComponent', () => {
  let component: NetComponent;
  let fixture: ComponentFixture<NetComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ NetComponent ],
      schemas: [CUSTOM_ELEMENTS_SCHEMA],
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(NetComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
