<?php return array (
  'problemid' => 'P1',
  'submitor' => 'test',
  'time' => 1700979871,
  'answer' => '#include<bits/stdc++.h>
using namespace std;
int nums[1024*1024*128/4+10];
int main(){
    int a,b;
    cin>>a>>b;
    for(int i=0;i<1024*1024*128/4+5;i++){nums[i]=i%600}
    cout<<a+b;
}',
  'score' => '0',
  'id' => 14,
  'status' => 'CE',
  'err' => 'ans.cpp: In function \'int main()\':
ans.cpp:13:55: error: expected \';\' before \'}\' token
',
);?>