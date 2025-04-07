/*
	Date: 2015
*/

#include <iostream>
#include <string>
using namespace std;

class Node{

public:

	Node *left;
	Node *Right;
	char value;

private:


};


class Tree {

public:
	Node *root;


	Tree() {
		root = NULL;
	}

void printInOrder(Node *temp);
void printPreOreder(Node *temp);
void printPostOrder(Node *temp);
void insert(char value);

};


void Tree::insert(char value) {

	Node *newNode = new Node();
	newNode->value = value;


	if (root == NULL) {
		root = newNode;
		return;
	}


	Node *temp = root;
	Node *prev = root;

	while (temp != NULL) {

		prev = temp;

		if (value > temp->value)
			temp = temp->Right;
		else
			temp = temp->left;

	
	}


	if (value > prev->value)
		prev->Right = newNode;
	else
		prev->left = newNode;

}


void Tree::printInOrder(Node *temp) {

	if (temp == NULL)
		return;


	printInOrder(temp->left);
	cout << " " << temp->value ;
	printInOrder(temp->Right);

}

void Tree::printPreOreder(Node *temp) {

	if (temp == NULL)
		return;


	cout << " " <<temp->value ;
	printPreOreder(temp->left);
	printPreOreder(temp->Right);

}


void Tree::printPostOrder(Node *temp) {

	if (temp == NULL)
		return;

	printPostOrder(temp->left);
	printPostOrder(temp->Right);
	cout<<" "<<temp->value ;
}


int main() {
	
	string s;
	char ch;
	cin >> s;

	Tree *Base = new Tree();


	while (cin >> s && !cin.eof()) {


		if (s == "I") {
			cin >> ch;
			Base->insert(ch);
		}
		else
			if (s == "PREFIXA") {

				Base->printPreOreder(Base->root);
				cout << "\n";

			
			}
			else 
				if (s == "INFIXA") {
				

					Base->printInOrder(Base->root);
					cout << "\n";

				
				}
				else if (s=="POSFIXA") {

					Base->printPostOrder(Base->root);
					cout << "\n";

				
				}
				else if (s == "P") {
					cin >> ch;
				 

				}
	
	}

	return 0;
}
