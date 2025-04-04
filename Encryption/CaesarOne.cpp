#include <iostream>
#include <string>
#include <cstring>
#include <cctype>
using namespace std;


string encryptText(string str , int key) {
  for (int index=0 ; index<str.size() ; index++)
  {
    if (isalpha(str[index]))
    {
      str[index] =(islower(str[index])) ? 
          char((int(str[index]-'a') + key) % 26) + 'a' 
        : 
          char((int(str[index]-'A') + key) % 26) + 'A'
        ;
    }
  }
  
	return str;
}

string decryptText(string str , int key) {

	for (int index=0 ; index<str.size() ; index++) 
	{
		if (isalpha(str[index]))
		{
		   str[index] = (islower(str[index])) ? 
		    char((int(str[index]-'z') - key) % 26) + 'z' 
		    :
		    char((int(str[index]-'Z') - key) % 26) + 'Z'
		    ;
		}
  }
  
	return str;
}



int main () {

	string plainText;	
	int key;

	cout<<"Enter text ::"<<endl;
	getline(cin , plainText);
	cout<<"Enter key  ::"<<endl;
	cin>>key;

	cout<<"After Encryption ::"<<endl;
	cout<<encryptText(plainText, key)<<endl;
	cout<<"After Decryption ::"<<endl;
	cout<<decryptText(encryptText(plainText, key), key);

	return 0;
}