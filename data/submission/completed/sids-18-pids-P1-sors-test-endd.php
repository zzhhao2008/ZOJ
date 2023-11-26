<?php return array (
  'problemid' => 'P1',
  'submitor' => 'test',
  'time' => 1700980095,
  'answer' => '#include<bits/stdc++.h>
using namespace std;
int nums[1024*1024*128/4+10];
int main(){
    int a,b;
    cin>>a>>b;
    for(int i=0;i<1024*1024*128/4+5;i++){nums[i]=i%600;}
    cout<<a+b;
}',
  'score' => '0',
  'id' => 18,
  'status' => 'AE',
  'err' => 'Accepted 0 of 6
Running:MLE in #0
Running:MLE in #1
Running:MLE in #2
Running:MLE in #3
Running:MLE in #4
Running:MLE in #5
',
);?>